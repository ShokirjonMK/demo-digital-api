import subprocess
import os
import sys

if (len(sys.argv) != 4):
  sys.exit('Installation failed: expecting 3 input arguments: OS type, slow query log path, EverSQL API key.')

OS_TYPE = sys.argv[1]
SLOW_QUERY_LOG_PATH = sys.argv[2]
API_KEY = sys.argv[3]

if not os.path.isfile(SLOW_QUERY_LOG_PATH) or not os.access(SLOW_QUERY_LOG_PATH, os.R_OK):
  sys.exit("Installation failed: no slow query log available in the provided path: " + SLOW_QUERY_LOG_PATH + ".")

if os.path.isfile('/usr/local/cpanel/cpanel'):
  sys.exit("Cpanel based environments are currently not supported. Please contact our support team for more information.")

package_check_retval = subprocess.call(["which", "setfacl"])
if package_check_retval != 0:
  sys.exit("Missing prerequisite: The sensor installation requires the acl package to set read permissions for slow query logs. For example, you can install it on Ubuntu using this command: sudo apt-get -y install acl")

download_link = ""
if (OS_TYPE == "ubuntu_14_04"):
  download_link = "https://toolbelt.treasuredata.com/sh/install-ubuntu-xenial-td-agent3.sh"
elif (OS_TYPE == "ubuntu_16_04"):
  download_link = "https://toolbelt.treasuredata.com/sh/install-ubuntu-xenial-td-agent3.sh"
elif (OS_TYPE == "ubuntu_18_04"):
  download_link = "https://toolbelt.treasuredata.com/sh/install-ubuntu-bionic-td-agent3.sh"
elif (OS_TYPE == "ubuntu_20_04"):
  download_link = "https://toolbelt.treasuredata.com/sh/install-ubuntu-focal-td-agent4.sh"
elif (OS_TYPE == "ubuntu_22_04"):
  download_link = "https://toolbelt.treasuredata.com/sh/install-ubuntu-jammy-td-agent4.sh"
elif (OS_TYPE == "centos"):
  download_link = "https://toolbelt.treasuredata.com/sh/install-redhat-td-agent3.sh"
elif (OS_TYPE == "debian_8"):
  download_link = "https://toolbelt.treasuredata.com/sh/install-debian-jessie-td-agent3.sh"
elif (OS_TYPE == "debian_9"):
  download_link = "https://toolbelt.treasuredata.com/sh/install-debian-stretch-td-agent3.sh"
elif (OS_TYPE == "debian_10"):
  download_link = "https://toolbelt.treasuredata.com/sh/install-debian-buster-td-agent3.sh"
elif (OS_TYPE == "debian_11"):
  download_link = "https://toolbelt.treasuredata.com/sh/install-debian-bullseye-td-agent4.sh"
elif (OS_TYPE == "redhat"):
  download_link = "https://toolbelt.treasuredata.com/sh/install-redhat-td-agent3.sh"
elif (OS_TYPE == "amazonlinux"):
  download_link = "https://toolbelt.treasuredata.com/sh/install-amazon2-td-agent4.sh"
else:
  sys.exit('Installation failed: invalid OS type inputted as the first argument.')

os.system('curl -L ' + download_link + ' > install_tdagent.sh')

subprocess.call(['sh', './install_tdagent.sh'])

open('/etc/td-agent/td-agent.conf', 'w').close()

new_conf_content = """<source>
  @type tail
  @id input_tail
  path {slow_log_path}
  pos_file /var/log/td-agent/slow.log.pos
  tag eversql.log
  read_from_head true
  <parse>
    @type none
    message_key slowlog
  </parse>
</source>
<filter eversql.**>
  @type record_transformer
  <record>
    host_param "#{{Socket.gethostname}}"
  </record>
</filter>
<match eversql.**>
  @type http
  endpoint https://actions.eversql.com/upload?apikey={api_key}
  http_method post
  content_type text/plain
  open_timeout 2
  <buffer>
    flush_interval 60s
    chunk_limit_size 3MB
  </buffer>
</match>""".format(slow_log_path=SLOW_QUERY_LOG_PATH, api_key=API_KEY)

conf_file = open('/etc/td-agent/td-agent.conf', 'a')
conf_file.write(new_conf_content)
conf_file.close()

os.system('setfacl -m u:td-agent:X /var/log/td-agent')

if os.path.isfile('/var/log/td-agent/slow.log.pos'):
   os.system('setfacl -m u:td-agent:rX /var/log/td-agent/slow.log.pos')

slow_query_log_path_parts = SLOW_QUERY_LOG_PATH.split("/")
incremental_slow_query_log_path = ""
for part in slow_query_log_path_parts:
  incremental_slow_query_log_path = incremental_slow_query_log_path + "/" + part
  os.system('setfacl -m u:td-agent:X ' + incremental_slow_query_log_path)

os.system('setfacl -m u:td-agent:rX ' + SLOW_QUERY_LOG_PATH)

os.system('sudo systemctl enable td-agent')
os.system('sudo systemctl restart td-agent')
os.system('rm install_tdagent.sh')