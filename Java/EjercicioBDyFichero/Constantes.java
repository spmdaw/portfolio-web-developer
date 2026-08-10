package examen3Eva;

public final class Constantes {

	
	private Constantes() {
		throw new UnsupportedOperationException("Esta clase no se puede instanciar");
	}
	
	public static final String USUARIO="root";
	public static final String CONTRASENA="";
    public static final String DB_HOST = "localhost";
    public static final String DB_PORT = "3306";
    public static final String DB_NAME = "instituto";
    public static final String DB_URL = "jdbc:mysql://" + DB_HOST + ":" + DB_PORT + "/" + DB_NAME + "?serverTimezone=UTC";
    

    public static final String rutaFichero="estadisticas/calificacion_estudiantes.txt";

}
