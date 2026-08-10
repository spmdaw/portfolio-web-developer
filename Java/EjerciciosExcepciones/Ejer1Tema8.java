package excepcionesEjer;

import java.util.InputMismatchException;
import java.util.Scanner;

public class Ejer1Tema8 {

//	Implementa un programa que pida al usuario un valor entero A utilizando
//	un nextInt() (de Scanner) y luego muestre por pantalla el mensaje “Valor
//	introducido: …”. Se deberá tratar la excepción InputMismatchException
//	que lanza nextInt() cuando no se introduce un entero válido. En tal caso se
//	mostrará el mensaje “Valor introducido incorrecto”

	public static void main(String[] args) {
		// TODO Auto-generated method stub

		Scanner teclado = new Scanner(System.in);

		try {
			System.out.println("Dime un valor entero");
			int valor = teclado.nextInt();
			System.out.println("El valor que has introducido es " + valor);
		} catch (InputMismatchException e) {

			System.out.println("Valor introducido incorrecto " + e.getMessage());
		}
		teclado.close();
	}
	

}
