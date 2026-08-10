package excepcionesEjer;

import java.util.InputMismatchException;
import java.util.Scanner;

public class Ejer2Tema8 {

//	Implementa un programa que pida dos valores int A y B utilizando un
//	nextInt() (de Scanner), calcule A/B y muestre el resultado por pantalla.
//	Se deberán tratar de forma independiente las dos posibles excepciones,
//	InputMismatchException y ArithmeticException
//	, mostrando en cada
//	caso un mensaje de error diferente en cada caso.

	public static void main(String[] args) {
		// TODO Auto-generated method stub
		Scanner teclado = new Scanner(System.in);

		try {
			System.out.println("Dime dos valores enteros para despues dividirlos");
			int valor1 = teclado.nextInt();
			int valor2 = teclado.nextInt();

			int division = valor1 / valor2;
			System.out.println("La division de : " + valor1 + " y  " + valor2 + "  es igual a : " + division);

		} catch (InputMismatchException e) {
			System.out.println("Los valores no son enteros " + e.getMessage());
		} catch (ArithmeticException e) {
			System.out.println("No puedes dividir entre 0 !!!" + e.getMessage());
		}

		teclado.close();
	}

}
