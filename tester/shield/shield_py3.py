# @file shield_py3.py

def shj_py3_shield():
  BLACKLIST = [
    '__import__', # deny importing modules
    'eval', # eval is evil
    'open',
    'file',
    'exec',
    'execfile',
    'compile',
    'reload',
    'reload',
    #'input'
    ]
  for func in BLACKLIST:
    if func in __builtins__.__dict__:
      del __builtins__.__dict__[func]

# If you want to prevent importing modules, you can
# import modules like "math" for students here:


# enabling shield:
shj_py3_shield()
