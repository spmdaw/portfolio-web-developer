package ejercicioAstros;

import java.util.ArrayList;

public class MainAstros {

	public static void main(String[] args) {
		// TODO Auto-generated method stub
		
		ArrayList<Planetas> arrayPlanetas= new ArrayList<>();
		
		Planetas planeta1= new Planetas("Jiomme", 5, "Bebesito", 2.500, 532, 10);
		Planetas planeta2 = new Planetas("COLIFLOR", 7, "Badbunny", 5, 14, 200.55);
		
		arrayPlanetas.add(planeta1);
		arrayPlanetas.add(planeta2);
		
		Satelites satelite1 = new Satelites("mamamio",30.22, "lalunayelsol", 2555, 15.2, 5222, 1536.2);
		planeta1.listaSatelites.add(satelite1);
		
		for (Planetas planetas : arrayPlanetas) {
			planetas.mostrar();
		}

	}

}
