package ejerciciosMapySet;

import java.util.HashMap;
import java.util.Scanner;

public class Ejer2Map {

	public static void main(String[] args) {
		// TODO Auto-generated method stub
		Scanner teclado = new Scanner(System.in);
		
		
//		Ejercicio 3: Actualizar información en un mapa
//		o Crea un Map que almacene estudiantes y sus calificaciones.
//		o Añade algunos estudiantes con calificaciones.
//		o Luego, permite actualizar la calificación de un estudiante existente.
//		o Si el estudiante no existe, muestra un mensaje diciendo que no se puede
//		encontrar.
		
		
		HashMap<String, Integer> mapa = new HashMap<>();
		mapa.put("Sonia", 7);
		mapa.put("Aaron", 9);
		mapa.put("Oscar", 6);
		
		mapa.put("Sonia", 8);
		
		System.out.println("Dime que estudiante quieres mirar su nota");
		String estudiante=teclado.next();
		for (String e: mapa.keySet()) {
			if (e.equals(estudiante)) {
				System.out.println("La nota de este estudiante es : " + mapa.get(e));
				return;
			}
		}
		System.out.println("El estudiante no se puede encontrar");
	}

}
