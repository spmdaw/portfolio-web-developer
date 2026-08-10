package ejerciciosMapySet;

import java.util.HashMap;
import java.util.Map;
import java.util.Scanner;

public class Ejer1Map {

	public static void main(String[] args) {
		// TODO Auto-generated method stub
		Scanner teclado = new Scanner(System.in);
//		Ejercicio 1: Crear un directorio telefónico
//		o Crea un Map donde las claves sean los nombres de personas y los valores sean
//		sus números de teléfono.
//		o Añade varios nombres y números al directorio.
//		o Permite al usuario consultar el número de teléfono de una persona, pidiendo
//		el nombre como entrada.

		HashMap<String, Integer> mapa = new HashMap<>();

		mapa.put("Sonia", 685963222);
		mapa.put("Maria", 652333658);
		mapa.put("Lucas", 652233659);

		System.out.println("Quiere consultar el numero de telefono de alguna persona en concreto?");
		String persona = teclado.next();
		for (String clave : mapa.keySet()) {
			if (clave.equals(persona)) {
				System.out.println("El numero de telefono de esta persona es " + mapa.get(clave));
				return;
			}

		}

	}

}
