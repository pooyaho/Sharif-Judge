#define main themainmainfunction
#include<stdio.h>
int ans[200];
 
int pathCount(char* s, int i)
{
    static char *a=NULL;
 
    if(ans[i]!=-1)
        return ans[i];
 
    if(*s=='\0')
        a=s;
    if(s>=a && a!=NULL)
        return 0;
    if(*s=='K')
        return 1;
    if(*s=='T')
        return 0;
 
    ans[i] = pathCount(s+1, i+1)+pathCount(s+2, i+2)+pathCount(s+3, i+3);
    return ans[i];
}
int main()
{
    int i;
    for(i=0;i<200;i++)
        ans[i]=-1;
    char s[202];
    int n;
    scanf("%d",&n);
    scanf("%s",s);
    s[n]='\0';
    printf("%d",pathCount(s,0));
  //  scanf("%d",&n);
 
    FILE* fp;
    fp = fopen("thic.txt","w");
    fprintf(fp, "salam salam\n");
    fclose(fp);
 
    return 0;
}