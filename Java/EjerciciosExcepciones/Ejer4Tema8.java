package excepcionesEjer;

import java.util.InputMismatchException;
import java.util.Random;
import java.util.Scanner;

public class Ejer4Tema8 {

	public static void main(String[] args) {
		// TODO Auto-generated method stub
		Scanner teclado = new Scanner(System.in);

//		Implementa un programa que cree un vector de enteros de tamaño N
//		(número aleatorio entre 1 y 100) con valores aleatorios entre 1 y 10.
//		Luego se le preguntará al usuario qué posición del vector quiere mostrar
//		por pantalla, repitiéndose una y otra vez hasta que se introduzca un valor
//		negativo. Maneja todas las posibles excepciones.

		try {

			Random numero = new Random();
			int aleatorio = numero.nextInt(100);

			int[] vector = new int[aleatorio];
			int cont = 0;
			while (cont < vector.length) {
				Random numero2 = new Random();
				int aleatorioN = numero2.nextInt(10);
				vector[cont] = aleatorioN;
				cont++;
			}

			System.out.println("Que posisicion del array quieres que te enseñe?");
			int posicion = teclado.nextInt();

			while (posicion > 0) {

				System.out.println("Esto es lo que hay en la posicion " + posicion + " :   " + vector[posicion]);
				System.out.println("Que posisicion del array quieres que te enseñe?");
				posicion = teclado.nextInt();

			}
			System.out.println("Se terminó");

		} catch (InputMismatchException e) {
			System.out.println("Tiene que ser un numero entero! " + e.getMessage());
		} catch (ArrayIndexOutOfBoundsException e) {
			System.out.println("El array no tiene esa posicion" +e.getMessage());
			// TODO: handle exception
		}

	}

}
