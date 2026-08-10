package excepcionesEjer;

import java.util.Scanner;

public class Ejer3Excepciones {

//	Objetivo: Usar varios bloques catch para diferentes excepciones.
//
//	👉 Enunciado:
//	Crea un programa que:
//
//	Declare un array de enteros.
//
//	Intente dividir un número por cero.
//
//	Intente acceder a un índice inválido del array.
//
//	Captura las excepciones por separado y muestra un mensaje diferente en cada caso.

	public static void main(String[] args) {
		// TODO Auto-generated method stub
		Scanner teclado = new Scanner(System.in);

		int[] arrayEnteros = new int[10];
		System.out.println("Dime un numero");
		int num = teclado.nextInt();
		System.out.println("Dime otro");
		int numDos = teclado.nextInt();

		try {

			double division = num / numDos;
			System.out.println("La division de esos dos numeros es de" + division);

		} catch (ArithmeticException e) {
			System.out.println("No se puede dividir entre 0");
		}

		try {
			System.out.println(arrayEnteros[11]);

		} catch (ArrayIndexOutOfBoundsException e) {
			System.out.println("Esa posicion del array no es valida");
		}

	}

}
