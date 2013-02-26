package Analyzing_simulation_program;

import java.awt.Container;
import java.awt.Dimension;
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
import javax.swing.JCheckBox;
import javax.swing.JFrame;
import javax.swing.JPanel;
import javax.swing.JProgressBar;
import javax.swing.JTextField;


public class Simulation_program extends JFrame implements ActionListener
{
	JPanel pane = new JPanel();
	JTextField textField;
	JProgressBar bar;
	JCheckBox sleepbox;
	String [] args;
	boolean running = false;
	boolean sleep = false;
	
	Simulation_program(String [] args) // the frame constructor method
	{
		super("Simulation program"); setBounds(100,100,300,200);
		this.args = args;
		setDefaultCloseOperation(JFrame.EXIT_ON_CLOSE);

		Container con = this.getContentPane(); // inherit main frame
		con.add(pane); // add the panel to frame
		textField = new JTextField(20);
		textField.setText("10");
		pane.add(textField);

		bar = new JProgressBar();
		bar.setPreferredSize(new Dimension(225, 15));
		bar.setStringPainted(true);
		pane.add(bar);

		JButton buttom = new JButton("Start");
		buttom.addActionListener(this);
		pane.add(buttom);
		
		sleepbox = new JCheckBox("Sleep for 200 mill.sek"); 
		pane.add(sleepbox);

		setVisible(true); // display this frame
	}
	public void actionPerformed(ActionEvent evt) {
		if(!running){
			running = true;
			String text = textField.getText();
			numberOfTrigerpoints = Integer.parseInt(text);
			if(sleepbox.isSelected())
				sleep=true;
			else
				sleep=false;
			
			Thread t = new Thread(new StartConection());
			t.start();
		}

	}

	private static int numberOfTrigerpoints;
	static int x;
	static int y; 
	static int z;
	static long timestamp;

	public static void main(String[] args) throws Exception {
		new Simulation_program(args);

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

	private class StartConection implements Runnable {

		@Override
		public void run() {
			try{
				String path = "http://127.0.0.1:8888/register/trigger/xml?";
				URL url;
				URLConnection urlConnection = null;
				DataInputStream inStream;

				bar.setMinimum(0);
				bar.setMaximum(numberOfTrigerpoints);
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
					if(sleep)
						Thread.sleep(200);
					bar.setValue(i);
				}
			}catch (Exception e) {}
			
//			OpenBrowser openBrowser = new OpenBrowser();
			URI uri = null;
			try {
				uri = new URI("http://127.0.0.1:8888");
			} catch (URISyntaxException e) {
				// TODO Auto-generated catch block
				e.printStackTrace();
			}
//			openBrowser.openWebpage(uri);
			running = false;
		}

	}

}




