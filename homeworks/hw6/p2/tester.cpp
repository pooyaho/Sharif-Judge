/*
 * IN THE NAME OF ALLAH
 *
 * Special Judge Script
 *
 */

#include <iostream>
#include <fstream>
#include <string>
using namespace std;
int main(int argc, char const *argv[])
{

	ifstream readin(argv[1]);
	ifstream readout(argv[2]);

	/* your judge script here */
	/*example: read n numbers and print their sum: */
	int n, a, sum=0;
	readin >> n;
	for (int i=0 ; i<n ; i++){
		readin >> a;
		sum += a;
	}
	int ans;
	readout >> ans;
	if (sum == ans)
		return 0;
	else
		return 1;
}