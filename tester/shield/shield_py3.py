# @file shield_py3.py

def shj_py3_shield():
  BLACKLIST = [
    #'__import__', # deny importing modules
    'eval', # eval is evil
    'open',
    'file',
    'exec',
    'execfile',
    'compile',
    'reload',
    #'input'
    ]
  for func in BLACKLIST:
    if func in __builtins__.__dict__:
      del __builtins__.__dict__[func]

import sys
sys.modules['os']=None

# enabling shield:
shj_py3_shield()
