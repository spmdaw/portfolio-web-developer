package excepcionesEjer;

import java.util.InputMismatchException;
import java.util.Scanner;

public class Ejer3Tema8 {

	public static void main(String[] args) {
		// TODO Auto-generated method stub

		Scanner teclado = new Scanner(System.in);
//		3. Implementa un programa que cree un vector tipo double de tamaño 5 y
//		luego, utilizando un bucle, pida cinco valores por teclado y los introduzca
//		en el vector. Tendrás que manejar la/las posibles excepciones y seguir
//		pidiendo valores hasta rellenar completamente el vector.

		try {

			double[] vector = new double[5];

			int cont = 0;

			while (cont < 5) {

				System.out.println("Dime un valor por teclado ");
				vector[cont] = teclado.nextDouble();
				cont++;

			}
			for (double d : vector) {
				System.out.println(d);
			}

		} catch (InputMismatchException e) {
			System.out.println("Tienen que ser numeros");

		} catch (IndexOutOfBoundsException e) {
			System.out.println("No se puede poner mas posiciones de lo que hay en el array  " + e.getMessage());
			// TODO: handle exception
		}

	}

}
