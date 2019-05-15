#!/usr/bin/env python

import os
file = open('temp.txt','r')
domain = file.readlines()
for a in domain:
    #print a
    cmd = "python subDomainsBrute.py  -t 120 "+a
    print cmd
    os.system(cmd)
