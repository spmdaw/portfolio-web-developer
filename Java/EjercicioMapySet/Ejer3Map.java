package ejerciciosMapySet;

import java.util.HashMap;
import java.util.Iterator;
import java.util.Scanner;

public class Ejer3Map {

	public static void main(String[] args) {
		// TODO Auto-generated method stub

		Scanner teclado = new Scanner(System.in);
//		Ejercicio 4: Eliminar entradas del mapa
//		o Crea un Map con nombres de productos y sus precios.
//		o Permite eliminar un producto dado su nombre y muestra el mapa actualizado.
//		o Si el producto no está en el mapa, muestra un mensaje indicando que no
//		existe.

		HashMap<String, Integer> mapa = new HashMap<>();
		mapa.put("Melon", 2);
		mapa.put("Yogures", 3);
		mapa.put("pan", 070);

		System.out.println("Que producto quieres eliminar");
		String producto = teclado.next();
		
		for (String e : mapa.keySet()) {
			if (e.equals(producto)) {
				mapa.remove(e);
				System.out.println("Borrado con exito");
				break;
			}
		}
		
		System.out.println("Te enseño como ha quedado la lista");
		for (String string : mapa.keySet()) {
			System.out.println(string +" " + mapa.get(string));
		}
	}

}
