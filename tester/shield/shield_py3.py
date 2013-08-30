# @file shield_py3.py

def shj_make_secure():
  UNSAFE = [
    'open',
    'file',
    'exec',
    'execfile',
    'compile',
    'reload',
    '__import__', #deny importing modules
    'reload',
    'eval', # eval is evil
    #'input'
    ]
  for func in UNSAFE:
    if func in __builtins__.__dict__:
      del __builtins__.__dict__[func]

# If you want to deny importing modules,
# You can import modules like "math" for students here

shj_make_secure()
