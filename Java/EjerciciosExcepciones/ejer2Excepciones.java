package excepcionesEjer;

import java.util.ArrayList;

//Objetivo: Manejar una excepción al acceder a un índice fuera de rango.
//👉 Enunciado:
//Declara un array con 3 elementos. Intenta acceder al índice 5 y captura la excepción adecuada para mostrar el mensaje: “Índice fuera del límite del array.”
public class ejer2Excepciones {

	public static void main(String[] args) {
		// TODO Auto-generated method stub

		int[] array = new int[3];
		array[0] = 5;
		array[1] = 10;

		try {
			System.out.println("La posicion 1 es " + array[0]);
			System.out.println("La posicion 5 es " + array[5]);

		} catch (ArrayIndexOutOfBoundsException e) {
			System.out.println("El array solo tiene 3 posiciones");
		}
	}

}
