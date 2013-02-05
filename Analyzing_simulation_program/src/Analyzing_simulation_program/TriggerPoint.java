package Analyzing_simulation_program;

import java.util.Date;

public class TriggerPoint {

	static int x;
	static int y; 
	static int z;
	static long timestamp;
	
	public TriggerPoint(){
		
	}

	@Override
	public String toString() {
		return "Trigerpoint: "+x+ ","+y+","+z+"," +timestamp;
	}
	
}
