package excepcionesEjer;

import java.util.Scanner;


//Objetivo: Capturar una excepción por división entre cero.
//
//👉 Enunciado:
//Crea un programa que pida dos números enteros y los divida. Si el divisor es 0, muestra un mensaje de error usando try-catch.

public class ejercicio1Excepciones {

	public static void main(String[] args) {
		// TODO Auto-generated method stub
		Scanner teclado = new Scanner(System.in);

		System.out.println("Ey hola dame un numero entero y despues otro");
		int numero = teclado.nextInt();
		System.out.println("Dame otro");
		int numeroDos = teclado.nextInt();

		try {
			double division = numero / numeroDos;
			System.out.println("La division es " + division);

		} catch (ArithmeticException e) {
			System.out.println("No se puede dividir entre 0");
		}

	}

}
