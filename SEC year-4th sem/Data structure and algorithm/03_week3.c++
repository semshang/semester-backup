#include <iostream>
using namespace std;

void info() {
    cout << "c++ programming!" << endl;
}

double add(double a, double b) {
    double sum = a + b;
    cout << a << " + " << b << " = " << sum << endl;
    return sum;
}

double subtract(double a, double b) {
    double sub = a - b;
    cout << a << " - " << b << " = " << sub << endl;
    return sub;
}

double multiply(double a, double b) {
    double mul = a * b;
    cout << a << " * " << b << " = " << mul << endl;
    return mul;
}

double divide(double a, double b) {
    if (b != 0) {
        double divided = a / b;
        cout << a << " / " << b << " = " << divided << endl;
        return divided;
    } else {
        cout << a << " / " << b << " = Error: Division by zero!" << endl;
        return 0;
    }
}

int main() {
    double sum = add(5, 40);
    double sub = subtract(5, 40);
    double mul = multiply(5, 40);
    double div = divide(5, 0);

    return 0;
}