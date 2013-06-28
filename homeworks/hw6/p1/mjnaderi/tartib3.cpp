#include <iostream>
#include <cstdio>
using namespace std;
int main (void){
    int n;
    cin >>n;
    int i, j, k, counter = 0;
    char a[1000], b[1000];
    for (i = 0; i < n; ++i){
        scanf ("%s", a);
        scanf ("%s", b);
        k = 0;
        counter = 0;
        for (j = 0; b[j] != '\0'; ++j){
            for (; a[k] != '\0' && b[j] != a[k]; ++k);
            if (b[j] == a[k]){
                ++counter;
                ++k;
            }
        }
        if (counter == j)
            printf ("YES\n");
        else
            printf ("NO\n");
    }
    return 0;
}