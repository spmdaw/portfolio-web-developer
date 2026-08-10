package excepcionesEjer;

import java.util.Scanner;

public class Ejer5Excepciones {

	public static void main(String[] args) {
		// TODO Auto-generated method stub

		Scanner teclado = new Scanner(System.in);

//		Objetivo: Encapsular el manejo de excepciones en un método.
//
//		👉 Enunciado:
//		Haz un método llamado dividir(int a, int b) que devuelva el resultado de la división. Si hay división por cero, captura la excepción y devuelve 0. Llama a ese método desde main.
//
//		Pista: Usa try-catch dentro del método, y devuelve un int.

		System.out.println("dame un numero");
		int numero = teclado.nextInt();
		System.out.println("Dame otro numero");
		int numeroDos = teclado.nextInt();
		
		dividir(numero,numeroDos);

	}

	public static int dividir(int a, int b) {
		try {
			int division = a / b;
			System.out.println("La division de estos numeros es de" + division);
			return division;
		} catch (ArithmeticException e) {
			System.out.println("No se puede dividir entre 0");
			return 0;
		}

	}

}
