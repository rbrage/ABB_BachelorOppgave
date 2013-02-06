package Analyzing_simulation_program;

import java.awt.Container;
import java.awt.event.ActionEvent;
import java.awt.event.ActionListener;
import java.io.DataInputStream;
import java.net.HttpURLConnection;
import java.net.URI;
import java.net.URISyntaxException;
import java.net.URL;
import java.net.URLConnection;
import java.util.Calendar;
import java.util.Random;

import javax.swing.JButton;
import javax.swing.JFrame;
import javax.swing.JPanel;
import javax.swing.JTextField;


public class Simulation_program extends JFrame implements ActionListener
{
	  JPanel pane = new JPanel();
	  JTextField textField;
	  String [] args;
	  Simulation_program(String [] args) // the frame constructor method
	  {
		  super("Simulation program"); setBounds(100,100,300,100);
	    this.args = args;
	    setDefaultCloseOperation(JFrame.EXIT_ON_CLOSE);
	    Container con = this.getContentPane(); // inherit main frame
	    con.add(pane); // add the panel to frame
	    textField = new JTextField(20);
	    textField.setText("10");
	    pane.add(textField);
	    JButton buttom = new JButton("Start");
	    buttom.addActionListener(this);
	    pane.add(buttom);
	    setVisible(true); // display this frame
	  }
	  public void actionPerformed(ActionEvent evt) {
	        String text = textField.getText();
	        numberOfTrigerpoints = Integer.parseInt(text);
	        startConection(numberOfTrigerpoints);
	        OpenBrowser openBrowser = new OpenBrowser();
	        URI uri = null;
			try {
				uri = new URI("http://127.0.0.1:8888");
			} catch (URISyntaxException e) {
				// TODO Auto-generated catch block
				e.printStackTrace();
			}
	        openBrowser.openWebpage(uri);
	        
	  }
	  
	private static int numberOfTrigerpoints;
	static int x;
	static int y; 
	static int z;
	static long timestamp;

	public static void main(String[] args) throws Exception {
		new Simulation_program(args);
		
	}
	
	public void startConection(int numberOfTrigerpoints){
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



