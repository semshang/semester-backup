#include <iostream>
using namespace std;

int main() {
    int arr[] = {1, 2, 3, 4, 5};
    int sum = 0;

    for (int i = 0; i < size(arr); i++) {
        sum = sum + arr[i];
    }

    cout << "Sum = " << sum << endl;
    return 0;
}
