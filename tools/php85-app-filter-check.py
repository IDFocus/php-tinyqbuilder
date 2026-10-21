"""Exercise the real NIVO filter controller with this checkout's Builder, using synthetic CGI input."""
from pathlib import Path
from urllib.parse import urlencode
import json, os, subprocess, sys, tempfile
package=Path(__file__).resolve().parents[1]
app=Path(sys.argv[1]).resolve()
cases=[
    ('wildcards','/identities',{'username':'john-doe_'},'username.kjohn-doe_'),
    ('dots','/identities',{'username':'first.last'},'username.kfirst....last'),
    ('combined filters','/identities',{'displayname':'A.B','username':'john-'},'displayname.kA....B.ousername.kjohn-'),
    ('equal field','/identities',{'status-account':'staged'},'status..account.estaged'),
    ('ignored input','/identities',{'unknown':'ignored','username':''},''),
    ('group filter','/groups',{'code':'team-','type':'manual'},'type.emanual.acode.kteam-'),
]
with tempfile.TemporaryDirectory(prefix='tinyq-filter-check-') as tmp:
    probe=Path(tmp)/'probe.php'
    probe.write_text('''<?php
error_reporting(E_ALL);
set_error_handler(static function($n,$s,$f,$l) { if ($n & error_reporting()) throw new ErrorException($s,0,$n,$f,$l); return false; });
require getenv('TINYQ_PACKAGE').'/src/TinyQ/Builder.php';
require getenv('NIVO_APP').'/vendor/autoload.php';
Monolog\\Registry::addLogger(new Monolog\\Logger('frontend',array(new Monolog\\Handler\\NullHandler())));
$f=new IDFocus\\Filter\\Controller(getenv('FILTER_PATH'));
echo json_encode($f->getParameters());
''')
    results=[]
    for label,path,params,expected in cases:
        env=dict(os.environ,REDIRECT_STATUS='1',REQUEST_METHOD='GET',QUERY_STRING=urlencode(params),
                 SCRIPT_FILENAME=str(probe),TINYQ_PACKAGE=str(package),NIVO_APP=str(app),FILTER_PATH=path)
        p=subprocess.run(['php-cgi','-d','error_reporting=-1','-d','display_errors=0','-d','log_errors=1','-d','output_buffering=0','-f',str(probe)],env=env,capture_output=True,text=True)
        _,sep,body=p.stdout.partition('\n\n')
        try:
            data=json.loads(body)
            actual=data.get('filter','') if isinstance(data,dict) else ('' if data==[] else None)
        except (ValueError,AttributeError):actual=None
        results.append(dict(case=label,expected=expected,actual=actual,passed=p.returncode==0 and not p.stderr and actual==expected,errors=p.stderr))
report=dict(php_cgi=subprocess.check_output(['php-cgi','-v'],text=True).splitlines()[0],passed=all(r['passed'] for r in results),cases=results,external_requests=False)
print(json.dumps(report,indent=2))
raise SystemExit(0 if report['passed'] else 1)
