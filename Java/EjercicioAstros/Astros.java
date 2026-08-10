package ejercicioAstros;

public abstract class Astros {
	
	public String masa;
	public double diametro;
	public String periodoRotacion;
	public double distancia;
	
	
	public Astros(String masa, double diametro, String periodoRotacion, double distancia) {
		this.masa = masa;
		this.diametro = diametro;
		this.periodoRotacion = periodoRotacion;
		this.distancia = distancia;
	}


	public String getMasa() {
		return masa;
	}


	public void setMasa(String masa) {
		this.masa = masa;
	}


	public double getDiametro() {
		return diametro;
	}


	public void setDiametro(double diametro) {
		this.diametro = diametro;
	}


	public String getPeriodoRotacion() {
		return periodoRotacion;
	}


	public void setPeriodoRotacion(String periodoRotacion) {
		this.periodoRotacion = periodoRotacion;
	}


	public double getDistancia() {
		return distancia;
	}


	public void setDistancia(double distancia) {
		this.distancia = distancia;
	}


	@Override
	public String toString() {
		return "Astros [masa=" + masa + ", diametro=" + diametro + ", periodoRotacion=" + periodoRotacion
				+ ", distancia=" + distancia + "]";
	}
	
	
	

}
