
def solve(s1, s2):
	i = 0
	d = 0
	for j in range(0, len(s2)):
		while s1[i] != s2[j]:
			i += 1
			if i >= len(s1):
				return False
	return True
#main
n = int(input())
for i in range(0, n):
	s1 = input()
	s2 = input()
	if solve(s1, s2) or solve(s1, s2[::-1]):
		print("YES")
	else:
		print("NO")