package ejerciciosMapySet;

import java.util.HashSet;

public class Ejer1Set {

	public static void main(String[] args) {
		// TODO Auto-generated method stub
		
//		Ejercicio 1: Eliminar duplicados de una lista
//		o Crea un Set e inserta algunos elementos de una lista de números (que
//		contenga duplicados).
//		o Muestra los elementos del Set después de insertar los elementos de la lista.
//		o Al final, el Set debe mostrar solo los elementos únicos
		
		HashSet<Integer> set = new HashSet<>();
		
		
		set.add(1);
		set.add(2);
		set.add(3);
		set.add(3);
		set.add(1);
		
		for (Integer integer : set) {
			System.out.println(integer);
		}
		
		
}

}
