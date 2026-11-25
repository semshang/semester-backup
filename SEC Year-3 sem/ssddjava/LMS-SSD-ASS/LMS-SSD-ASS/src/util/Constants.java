package util;

/**
 * Database connection settings.
 *
 * Override values with environment variables or JVM system properties:
 *   -DB_HOST, -DB_PORT, -DB_NAME, -DB_USER, -DB_PASSWORD
 * or set DB_HOST, DB_PORT, DB_NAME, DB_USER, DB_PASSWORD in the environment.
 */
public final class Constants {
    public static final String DB_HOST = getSetting("DB_HOST", "localhost");
    public static final String DB_PORT = getSetting("DB_PORT", "3306");
    public static final String DB_NAME = getSetting("DB_NAME", "smartlib");
    public static final String DB_USER = getSetting("DB_USER", "root");
    public static final String DB_PASSWORD = getSetting("DB_PASSWORD", "root");
    public static final String DB_PARAMETERS = "?useSSL=false&allowPublicKeyRetrieval=true&serverTimezone=UTC";
    public static final String DB_URL_BASE = "jdbc:mysql://" + DB_HOST + ":" + DB_PORT + "/" + DB_PARAMETERS;
    public static final String DB_URL = "jdbc:mysql://" + DB_HOST + ":" + DB_PORT + "/" + DB_NAME + DB_PARAMETERS;

    private Constants() {
    }

    private static String getSetting(String key, String defaultValue) {
        String value = System.getProperty(key);
        if (value != null && !value.isBlank()) {
            return value;
        }
        value = System.getenv(key);
        return value != null && !value.isBlank() ? value : defaultValue;
    }
}
