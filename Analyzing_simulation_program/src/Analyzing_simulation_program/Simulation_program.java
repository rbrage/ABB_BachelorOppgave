package Analyzing_simulation_program;

import java.io.DataInputStream;
import java.net.HttpURLConnection;
import java.net.URL;
import java.net.URLConnection;
import java.util.Calendar;
import java.util.Random;


public class Simulation_program {

	private final static int numberOfTrigerpoints = 10;
	static int x;
	static int y; 
	static int z;
	static long timestamp;


	public static void main(String[] args) throws Exception {
		
		try{
			String path = "http://127.0.0.1:8888/register/trigger/xml?";
			URL url;
			URLConnection urlConnection = null;
			DataInputStream inStream;

			int i=0;
			while(i<numberOfTrigerpoints){
				setNewTriggerpoint();
				url = new URL(path+"x="+x+"&y="+y+"&z="+z+"&time="+timestamp);
				urlConnection = url.openConnection();
				((HttpURLConnection)urlConnection).setRequestMethod("POST");
				urlConnection.setDoOutput(true);
				System.out.println(url.getQuery());

				inStream = new DataInputStream(urlConnection.getInputStream());

				String buffer;
				while((buffer = inStream.readLine()) != null) {
					if(buffer.contains("false")){
						System.out.println(buffer);
					}
				}
				inStream.close();
				i++;
			}
		}catch (Exception e) {}
	}

	public static void setNewTriggerpoint(){
		x = RandomNumber();
		y = RandomNumber();
		z = RandomNumber();
		timestamp = getTime();
	}
	public static int RandomNumber(){
		Random random = new Random(); 
		int number = random.nextInt(100);
		return number;

	}
	public static long getTime(){
		Calendar cal = Calendar.getInstance();
		return cal.getTimeInMillis();
	}
}
