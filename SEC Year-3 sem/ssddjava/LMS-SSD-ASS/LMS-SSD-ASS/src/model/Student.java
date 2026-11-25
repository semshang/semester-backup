package model;

public class Student {
    private String registrationNo;
    private String name;
    private String mobileNo;
    private String branch;
    private String email;

    public Student() {
    }

    public Student(String registrationNo, String name, String mobileNo, String branch) {
        this.registrationNo = registrationNo;
        this.name = name;
        this.mobileNo = mobileNo;
        this.branch = branch;
    }

    public Student(String registrationNo, String name, String mobileNo, String branch, String email) {
        this.registrationNo = registrationNo;
        this.name = name;
        this.mobileNo = mobileNo;
        this.branch = branch;
        this.email = email;
    }

    public String getRegistrationNo() {
        return registrationNo;
    }

    public void setRegistrationNo(String registrationNo) {
        this.registrationNo = registrationNo;
    }

    public String getName() {
        return name;
    }

    public void setName(String name) {
        this.name = name;
    }

    public String getMobileNo() {
        return mobileNo;
    }

    public void setMobileNo(String mobileNo) {
        this.mobileNo = mobileNo;
    }

    public String getBranch() {
        return branch;
    }

    public void setBranch(String branch) {
        this.branch = branch;
    }

    public String getEmail() {
        return email;
    }

    public void setEmail(String email) {
        this.email = email;
    }
}
