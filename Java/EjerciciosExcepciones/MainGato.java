package excepcionesEjer;

import java.util.Scanner;

public class MainGato {

	public static void main(String[] args) {
		// TODO Auto-generated method stub

		Scanner teclado= new Scanner(System.in);
		
		Gato gato1= new Gato("Popo", 5);
		try {
			gato1.comprobarNombre();
			gato1.imprimir();
			
		} catch (Nombre3CaractException e) {
			
			System.out.println("El nombre no tiene 3 caracteres   " +e.getMessage());
		}
	}

}
