package ejercicioAstros;

import java.util.ArrayList;

public class Planetas extends Astros implements Mostrar {

	private double distanciaSol;
	private double orbitaSol;
	ArrayList<Satelites> listaSatelites = new ArrayList<>();

	public Planetas(String masa, double diametro, String periodoRotacion, double distancia, double distanciaSol,
			double orbitaSol) {
		super(masa, diametro, periodoRotacion, distancia);
		// TODO Auto-generated constructor stub
		this.distanciaSol = distanciaSol;
		this.orbitaSol = orbitaSol;
	}

	@Override
	public String toString() {
		return "Planetas [distanciaSol=" + distanciaSol + ", orbitaSol=" + orbitaSol + ", masa=" + masa + ", diametro="
				+ diametro + ", periodoRotacion=" + periodoRotacion + ", distancia=" + distancia + "]";
	}

	public void mostrar() {
		System.out.println(this.toString());
		
		if(listaSatelites.size() != 0) {
			for (Satelites satelites : listaSatelites) {
				System.out.println(satelites);
				
			}
		}
	}
	


}
