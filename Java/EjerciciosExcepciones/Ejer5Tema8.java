package excepcionesEjer;

public class Ejer5Tema8 {

//	 Implementa un programa con tres funciones:
//		 ◦ void imprimePositivo(int p): Imprime el valor p. Lanza una
//		 ‘Exception’ si p < 0
//		 ◦ void imprimeNegativo(int n): Imprime el valor n. Lanza una
//		 ‘Exception’ si p >= 0
//		 ◦ La función main para realizar pruebas. Puedes llamar a ambas
//		 funciones varias veces con distintos valores, hacer un bucle para pedir
//		 valores por teclado y pasarlos a las funciones, etc. Maneja las posibles
//		 excepciones

	public static void main(String[] args) {
		// TODO Auto-generated method stub

		try {
			imprimePositivo(20);
			imprimeNegativo(20);

		} catch (MiExcepcionEdadPositiva e) {
			System.out.println("Error" + e.getMessage());
		} catch (MiExcepcionNegativa e) {
			System.out.println("ERROR" + e.getMessage());
		}
	}

	public static void imprimePositivo(int p) throws MiExcepcionEdadPositiva {
		if (p < 0) {
			throw new MiExcepcionEdadPositiva("Es negativo y debe ser positivo");
		}

		System.out.println("El valor que me has dado es " + p);
	}

	public static void imprimeNegativo(int n) throws MiExcepcionNegativa {

		if (n > 0) {
			throw new MiExcepcionNegativa("De ser positiva y es negativa");

		}
		System.out.println("El valor es  " + n);
	}

}
