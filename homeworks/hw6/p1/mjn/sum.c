// In the name of Allah
#include <stdio.h>
int main(){
	int n;
	scanf("%d",&n);
	int sum=0,i;
	for (i=0 ; i<n ; i++){
		int a;
		scanf("%d",&a);
		sum += a;
	}
	printf("%d\n", sum);

	return 0;
}