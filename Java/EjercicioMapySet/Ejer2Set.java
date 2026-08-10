package ejerciciosMapySet;

import java.util.HashSet;
import java.util.Scanner;

public class Ejer2Set {

	public static void main(String[] args) {
		// TODO Auto-generated method stub
		Scanner teclado = new Scanner(System.in);
//		
//		Ejercicio 2: Comprobar si un elemento existe en un conjunto
//		o Crea un Set que contenga algunos nombres de países.
//		o Pide al usuario que ingrese un país y muestra un mensaje indicando si el país
//		existe en el Set o no

		HashSet<String> set = new HashSet<>();

		set.add("Francia");
		set.add("Italia");
		set.add("Alemania");
		set.add("Noruega");

		System.out.println("Ingresa un pais yo te digo si esta entre nuestra lista o no ");
		String pais = teclado.next();

		for (String string : set) {
			if (string.equalsIgnoreCase(pais)) {
				System.out.println("SI EXISTE");
				return;
			}
		}
		System.out.println("NO EXISTE");
	}

}
