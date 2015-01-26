#!/usr/bin/python
import sys

log_name= "pomme_tool_log.txt";

if len(sys.argv) > 1:
  url = sys.argv[1]
  f = open(log_name, "a")
  f.write(url + "\n")
  f.close();

f = open(log_name)
lines = f.readlines()
for line in lines:
  line = line.strip()
  has_title = line.find("#") != -1 
  if has_title:
    title = line.split("#")[1].split("http://")[0]
    content = "%s: <a href='%s'>%s</a>" % (title, line, line)
  else:
    content = "<a href='%s'>%s</a>" % (line, line)
  list_item = "<li>%s</li>" % (content)
  print list_item
