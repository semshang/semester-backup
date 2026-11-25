package model;

public class Book extends LibraryItem {
    private String category;
    private double price;
    private int quantity;
    private int availableQuantity;

    public Book() {
        super();
    }

    public Book(String id, String title, String author, String category, double price, int quantity, int availableQuantity) {
        super(id, title, author);
        this.category = category;
        this.price = price;
        this.quantity = quantity;
        this.availableQuantity = availableQuantity;
    }

    public String getCategory() {
        return category;
    }

    public void setCategory(String category) {
        this.category = category;
    }

    public double getPrice() {
        return price;
    }

    public void setPrice(double price) {
        this.price = price;
    }

    public int getQuantity() {
        return quantity;
    }

    public void setQuantity(int quantity) {
        this.quantity = quantity;
    }

    public int getAvailableQuantity() {
        return availableQuantity;
    }

    public void setAvailableQuantity(int availableQuantity) {
        this.availableQuantity = availableQuantity;
    }

    @Override
    public String getItemType() {
        return "Book";
    }
}
