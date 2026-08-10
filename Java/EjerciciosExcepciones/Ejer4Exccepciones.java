package excepcionesEjer;

import java.util.Scanner;

public class Ejer4Exccepciones {

	public static void main(String[] args) {
		// TODO Auto-generated method stub

		Scanner teclado = new Scanner(System.in);
//		Objetivo: Usar el bloque finally correctamente.
//
//		👉 Enunciado:
//		Haz un programa que intente abrir un número y dividir 100 entre él. Siempre debe imprimirse “Proceso terminado” al final, independientemente de si hubo o no un error.
//
//		Pista: Mete el System.out.println("Proceso terminado"); en el finally.

		System.out.println("Dime un numero que dividiremos despues");
		double numero = teclado.nextInt();

		try {
			double division = numero / 100;
			System.out.println("La division es de " + division); //COMO ESTO NO SALTA UNA EXCEPCION NO SE PONE EL CATCH YA QUE QUEREMOS USAR FINALLY Y FINALLY NO SE PUEDE USAR SOLO.

		} finally {
			System.out.println("Proceso terminado");
		}
	}

}
