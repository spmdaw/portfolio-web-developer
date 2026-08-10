package examenFinal2Trimestre;

import empresaYempleados.Empleado;

public class Director extends Empleados implements Mandador {

	CoordinadorTecnico coordinadorTecnico;
	CoordinadorVentas coordinadorVentas;

	public Director(int id, String nombre, CoordinadorTecnico cT, CoordinadorVentas cV) {
		super(id, nombre);
		// TODO Auto-generated constructor stub
		this.coordinadorTecnico = cT;
		this.coordinadorVentas = cV;
	}

	@Override
	public String toString() {
		return "Director [precioHora=" + Constantes.PRECIO_HORA_DIRECTOR + ", coordinadorTecnico=" + coordinadorTecnico
				+ ", coordinadorVentas=" + coordinadorVentas + ", getAnyos_ant()=" + getAnyos_ant() + ", getId()="
				+ getId() + ", getNombre()=" + getNombre() + "]";
	}

	@Override
	public void mostrarInfo() {
		System.out.println(toString());
	}

	public double calculoNominaDirector() {
		int Horas = 40;
		double nomina = Horas * Constantes.PRECIO_HORA_DIRECTOR * 4;

		return nomina;
	}
	
	public boolean mandarOrden(String descripcion,int tipo, int dificultad) {
		
		if (tipo==1) {
			return coordinadorTecnico.mandarOrden(descripcion,tipo,dificultad);
		}if (tipo==2) {
			return coordinadorVentas.mandarOrden(descripcion,tipo, dificultad);
		}
		return false;
		
	}
}
