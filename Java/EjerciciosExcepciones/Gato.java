package excepcionesEjer;

public class Gato {

//	
//	Implementa una clase Gato con los atributos nombre y edad, un
//	constructor con parámetros, los getters y setters, además de un método
//	imprimir() para mostrar los datos de un gato. El nombre de un gato debe
//	tener al menos 3 caracteres y la edad no puede ser negativa. Por ello,
//	tanto en el constructor como en los setters, deberás comprobar que los
//	valores sean válidos y lanzar una ‘Exception’ si no lo son. Luego, haz una
//	clase principal con main para hacer pruebas: instancia varios objetos
//	Gato y utiliza sus setters, probando distintos valores (algunos válidos y
//	otros incorrectos). Maneja las excepciones.

	private String nombre;
	private int edad;

	public Gato(String nombre, int edad) {
		this.nombre = nombre;
		this.edad = edad;
	}

	public String getNombre() {
		return nombre;
	}

	public void setNombre(String nombre) {
		this.nombre = nombre;
	}

	public int getEdad() {
		return edad;
	}

	public void setEdad(int edad) {
		this.edad = edad;
	}

	@Override
	public String toString() {
		return "Gato [nombre=" + nombre + ", edad=" + edad + "]";
	}

	public void imprimir() {
		System.out.println(toString());
	}

	public void comprobarNombre() throws Nombre3CaractException {
		if (nombre.length()>3) {
			throw new Nombre3CaractException("Demasiado largo el nombre illo");
		}
	}
	

}
