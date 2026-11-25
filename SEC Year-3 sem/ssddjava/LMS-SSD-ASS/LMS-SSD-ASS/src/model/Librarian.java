package model;

public class Librarian extends User {
    public Librarian() {
        super();
        setRole("LIBRARIAN");
    }

    public Librarian(int userId, String username, String password) {
        super(userId, username, password, "LIBRARIAN");
    }

    public Librarian(String username, String password) {
        super(username, password, "LIBRARIAN");
    }
}
