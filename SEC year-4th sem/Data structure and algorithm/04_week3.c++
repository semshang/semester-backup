#include <iostream>
using namespace std;

char findMax(char a, char b, char c)
{
    char max = a;

    if (b > max)
    {
        max = b;
    }

    if (c > max)
    {
        max = c;
    }

    return max;
}

int main()
{
    char ch1, ch2, ch3;

    cout << "Enter three alphabets: ";
    cin >> ch1 >> ch2 >> ch3;

    char result = findMax(ch1, ch2, ch3);

    cout << "The maximum alphabet is: " << result << endl;

    return 0;
}