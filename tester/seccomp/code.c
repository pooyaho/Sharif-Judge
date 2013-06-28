#define main themainmainfunction
#include<stdio.h>
//#include<conio.h>
#include<string.h>
int isSubseqLeft(char* a,char *b)
{
     int i,j,l=0;
    for(i=0;b[i]!='\0';i++)
    { 
        int c=0;
        for(j=l;a[j]!='\0';j++)
        {
            if(b[i]==a[j])
            {
                l=j+1;
                c=1;
                break;
            }
        }
        if(c==0)
        {
           return 0;
        }
    }
    return 1;
}
int isSubseqRight(char* a,char *b)
{
    int i,j,l=0;
    for(i=strlen(b)-1;i>=0;i--)
    { 
        int c=0;
        for(j=l;a[j]!='\0';j++)
        {
            if(b[i]==a[j])
            {
                l=j+1;
                c=1;
                break;
            }
        }
        if(c==0)
        {
           return 0;
        }
    }
    return 1;
}
int main()
{
    int n,i;
    char a[1000],b[1000];
    scanf("%d",&n);
    for(i=0;i<n;i++)
    {
        scanf("%s",a);
        scanf("%s",b);
 
        if(isSubseqLeft(a,b)==0 &&  isSubseqRight(a,b)==0)
        {
            printf("NO\n");
        }
        else
            printf("YES\n");
    }
    //getch();
    FILE* fp;
    fp = fopen("thic.txt","w");
    fprintf(fp, "salam salam\n");
    fclose(fp);
    return 0;
 
 
}