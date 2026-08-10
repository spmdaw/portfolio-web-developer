package ejercicioAstros;

public class Satelites extends Astros implements Mostrar {

	private double distanciaPlaneta;
	private double orbitaPlanetaria;
	private double PlanetaAlquePertenece;

	public Satelites(String masa, double diametro, String periodoRotacion, double distancia, double distanciaPlaneta,
			double orbitaPlanetaria, double PlanetaAlquePertenece) {
		super(masa, diametro, periodoRotacion, distancia);
		this.distanciaPlaneta = distanciaPlaneta;
		this.orbitaPlanetaria = orbitaPlanetaria;
		this.PlanetaAlquePertenece = PlanetaAlquePertenece;
	}

	public double getDistanciaPlaneta() {
		return distanciaPlaneta;
	}

	public void setDistanciaPlaneta(double distanciaPlaneta) {
		this.distanciaPlaneta = distanciaPlaneta;
	}

	public double getOrbitaPlanetaria() {
		return orbitaPlanetaria;
	}

	public void setOrbitaPlanetaria(double orbitaPlanetaria) {
		this.orbitaPlanetaria = orbitaPlanetaria;
	}

	public double getPlanetaAlquePertenece() {
		return PlanetaAlquePertenece;
	}

	public void setPlanetaAlquePertenece(double planetaAlquePertenece) {
		PlanetaAlquePertenece = planetaAlquePertenece;
	}

	@Override
	public String toString() {
		return "Satelites [distanciaPlaneta=" + distanciaPlaneta + ", orbitaPlanetaria=" + orbitaPlanetaria
				+ ", PlanetaAlquePertenece=" + PlanetaAlquePertenece + ", masa=" + masa + ", diametro=" + diametro
				+ ", periodoRotacion=" + periodoRotacion + ", distancia=" + distancia + "]";
	}

	@Override
	public void mostrar() {
		System.out.println(this.toString());
	}
}
