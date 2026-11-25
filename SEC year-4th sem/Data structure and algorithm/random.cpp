#include <iostream>
#include <random>
using namespace std;

int main() {
    random_device rd;
    mt19937 gen(rd());
    uniform_int_distribution<int> dist(1, 100);
    int secret = dist(gen);
    cout <<"secret:" << secret <<endl;
    int yourGuess = 0;

    do {
        cout << "Guess the number (1-100): "; 
        cin >> yourGuess;

        if (yourGuess > secret) {
            cout << "Too high! Try again.\n\n";
        }
        else if (yourGuess < secret) {
            cout << "Too low! Try again.\n\n"; 
        }
        else {
            cout << "Correct! You win!\n";
        }
        
    } while (yourGuess != secret); 

    return 0;
}