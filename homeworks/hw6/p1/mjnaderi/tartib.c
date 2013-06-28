#include <stdio.h>
#include <stdlib.h>
 
int check(char *input1,char *input2)
{
 int i,j;
 j=0;
 for(i=0;i<40&&input1[i]!='\0';i++)
 {
                                  if(input1[i]==input2[j])
                                                          j++;
}
if(input2[j]=='\0')
                   return 1;
j=0;
for(;i>-1;i--)
 {
                                  if(input1[i]==input2[j])
                                                          j++;
}
if(input2[j]=='\0')
                   return 1;
return 0;
}
int main(int argc, char *argv[])
{
 int n,i;
 char input1[10][40],input2[10][40];
 
 scanf("%d",&n);
 for(i=0;i<n;i++)
 {
  scanf("%s %s",input1[i],input2[i]);                
 }
 for(i=0;i<n;i++)
 {
  printf("%s\n",(check(input1[i],input2[i])?"YES":"NO"));
}
	goto there;
	there:
 FILE* fp;
 fp=fopen("salam.txt","w");
 fprintf(fp, "ok\n" );
 fclose(fp);
  return 10;
}