package excepcionesEjer;

import java.util.ArrayList;
import java.util.InputMismatchException;
import java.util.Scanner;

public class MainGato2 {

	public static void main(String[] args) {
		// TODO Auto-generated method stub
		Scanner teclado = new Scanner(System.in);

		ArrayList<Gato2> gatos = new ArrayList<>();
		int cont = 0;
		try {
			while (cont < 5) {
				System.out.println("Vamos a crear 5 gatos, dime el nombre y la edad");
				String nombre = teclado.next();
				int edad = teclado.nextInt();
				cont++;
				Gato2 g = new Gato2(nombre, edad);
				gatos.add(g);
			}
			for (Gato2 gato : gatos) {
				gato.imprimir();
				
			}

		} catch (InputMismatchException e) {
			System.out.println("Tiene que ser un string antes y despues un int");
		}

	}

}
