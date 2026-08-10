package examenFinal2Trimestre;

import java.util.ArrayList;
import java.util.Random;

public class CoordinadorTecnico extends Empleados implements Mandador {

	ArrayList<Tecnico> empleadosTecnicos = new ArrayList<>();

	public CoordinadorTecnico(int id, String nombre) {
		super(id, nombre);
		// TODO Auto-generated constructor stub
	}

	@Override
	public String toString() {
		return "CoordinadorTecnico [precioHora=" + Constantes.PRECIO_HORA_COORDINADOR_TECNICO + ", getAnyos_ant()=" + getAnyos_ant() + ", getId()="
				+ getId() + ", getNombre()=" + getNombre() + "]";
	}

	@Override
	public void mostrarInfo() {
		System.out.println(toString());
	}

	public void añadirEmpleadosTecnicos(Tecnico tecnico) {
		empleadosTecnicos.add(tecnico);
	}

	public double calculoNominaCT() {
		int Horas = 40;
		double nomina = Horas * Constantes.PRECIO_HORA_COORDINADOR_TECNICO * 4;

		return nomina;
	}

	public boolean mandarOrden(String descripcion,int tipo, int dificultad) {
		for (Tecnico tecnico : empleadosTecnicos) {

			if (dificultad >= tecnico.getExperiencia()) {
				return tecnico.realizarTarea(descripcion);
			}
		}
		Random numero = new Random();
		int aleatorio = numero.nextInt(empleadosTecnicos.size());
		return empleadosTecnicos.get(aleatorio).realizarTarea(descripcion);

	}
}
