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
import javax.swing.JFrame;
import javax.swing.JLabel;
import javax.swing.JPanel;
import javax.swing.JProgressBar;
import javax.swing.JTextField;


public class Simulation_program extends JFrame implements ActionListener
{
	JPanel pane = new JPanel();
	JTextField numPointsField;
	JTextField xSentrumField;
	JTextField ySentrumField;
	JTextField zSentrumField;
	JTextField xRangeField;
	JTextField yRangeField;
	JTextField zRangeField;
	JTextField sleepField;
	JProgressBar bar;
	String [] args;
	boolean running = false;
	Thread t;
	
	Simulation_program(String [] args) // the frame constructor method
	{
		super("Simulation program"); 
		setBounds(100,100,300,300);
		this.args = args;
		setDefaultCloseOperation(JFrame.EXIT_ON_CLOSE);

		Container con = this.getContentPane(); // inherit main frame
		con.add(pane); // add the panel to frame
				
		pane.add(new JLabel("(x,y,z) \\ (Sentrum, Range):"));
		
		xSentrumField = new JTextField(14);
		xSentrumField.setText("50");
		pane.add(xSentrumField);
		xRangeField = new JTextField(5);
		xRangeField.setText("10");
		pane.add(xRangeField);
		
		ySentrumField = new JTextField(14);
		ySentrumField.setText("50");
		pane.add(ySentrumField);
		yRangeField = new JTextField(5);
		yRangeField.setText("10");
		pane.add(yRangeField);
		
		zSentrumField = new JTextField(14);
		zSentrumField.setText("50");
		pane.add(zSentrumField);
		zRangeField = new JTextField(5);
		zRangeField.setText("10");
		pane.add(zRangeField);
		
		pane.add(new JLabel("Number of points to submit:"));
		
		numPointsField = new JTextField(20);
		numPointsField.setText("10");
		pane.add(numPointsField);
		
		pane.add(new JLabel("Time between points:"));
		
		sleepField = new JTextField(20);
		sleepField.setText("200");
		pane.add(sleepField);

		bar = new JProgressBar();
		bar.setPreferredSize(new Dimension(225, 15));
		bar.setStringPainted(true);
		pane.add(bar);

		JButton start = new JButton("Start");
		start.addActionListener(this);
		pane.add(start);
		
		JButton stop = new JButton("Stop");
		stop.addActionListener(new ActionListener(){

			@Override
			public void actionPerformed(ActionEvent arg0) {
				if(t != null){
					t.interrupt();
				}
			}
			
		});
		pane.add(stop);

		setVisible(true); // display this frame
	}
	public void actionPerformed(ActionEvent evt) {
		if(!running){
			running = true;

			xSentrum = Integer.parseInt(xSentrumField.getText());
			xRange = Integer.parseInt(xRangeField.getText());
			
			ySentrum = Integer.parseInt(ySentrumField.getText());
			yRange = Integer.parseInt(yRangeField.getText());
			
			zSentrum = Integer.parseInt(zSentrumField.getText());
			zRange = Integer.parseInt(zRangeField.getText());
			
			String text = numPointsField.getText();
			numberOfTrigerpoints = Integer.parseInt(text);
			
			timeToSleep = Integer.parseInt(sleepField.getText());
			
			t = new Thread(new StartConection());
			t.start();
		}

	}

	private static int numberOfTrigerpoints;
	static int x;
	static int y; 
	static int z;
	static int xRange;
	static int yRange; 
	static int zRange;
	static int xSentrum;
	static int ySentrum; 
	static int zSentrum;
	static long timestamp;
	static int timeToSleep;

	public static void main(String[] args) throws Exception {
		new Simulation_program(args);

	}

	public void setNewTriggerpoint(){
		x = RandomClusterNumber(xSentrum, xRange);
		y = RandomClusterNumber(ySentrum, yRange);
		z = RandomClusterNumber(zSentrum, zRange);
		timestamp = getTime();
	}
	public int RandomNumber(int max){
		Random random = new Random(); 
		return random.nextInt(max);

	}
	
	public int RandomClusterNumber(int base, int maxRange){
		return base + (RandomNumber(maxRange*2) - maxRange);
	}
	
	public long getTime(){
		Calendar cal = Calendar.getInstance();
		return cal.getTimeInMillis();
	}
	
	
	private class StartConection implements Runnable {

		@Override
		public void run() {
			try{
				String path = "http://abb.hf-data.no/register/trigger/xml?";
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

//					String buffer;
//					while((buffer = inStream.readLine()) != null) {
//						if(buffer.contains("false")){
//							System.out.println(buffer);
//						}
//					}
					inStream.close();
					i++;
					Thread.sleep(timeToSleep);
					bar.setValue(i);
					
					if(t.isInterrupted()) break;
				}
			}catch (Exception e) {}
			
//			OpenBrowser openBrowser = new OpenBrowser();
//			URI uri = null;
//			try {
//				uri = new URI("http://127.0.0.1:8888");
//			} catch (URISyntaxException e) {
//				// TODO Auto-generated catch block
//				e.printStackTrace();
//			}
//			openBrowser.openWebpage(uri);
			running = false;
		}

	}

}




