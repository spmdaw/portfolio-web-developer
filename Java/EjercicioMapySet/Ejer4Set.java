package ejerciciosMapySet;

import java.util.HashSet;
import java.util.Iterator;

public class Ejer4Set {

	public static void main(String[] args) {
		// TODO Auto-generated method stub

//		Ejercicio 4: Encontrar la intersección de dos conjuntos
//		o Crea dos Set de nombres.
//		o Calcula e imprime la intersección de ambos conjuntos, es decir, los nombres
//		que están en ambos.

		HashSet<String> nombres1 = new HashSet<>();
		HashSet<String> nombres2 = new HashSet<>();

		nombres1.add("Sonia");
		nombres1.add("Oscar");
		nombres1.add("Hugo");
		nombres1.add("Pablo");
		nombres1.add("Aaron");

		nombres2.add("Alvaro");
		nombres2.add("Jose");
		nombres2.add("Irene");
		nombres2.add("Oscar");
		nombres2.add("Hugo");
		nombres2.add("Pablo");

		System.out.println("A continuacion solo mostrare los repetidos en las dos listas");

		nombres1.retainAll(nombres2);
		for (String string : nombres1) {
			System.out.println(string);

		}
	}

}
