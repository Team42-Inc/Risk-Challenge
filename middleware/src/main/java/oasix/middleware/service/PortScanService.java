package oasix.middleware.service;

import java.io.BufferedReader;
import java.io.IOException;
import java.io.InputStreamReader;
import java.util.ArrayList;
import java.util.List;
import java.util.Scanner;

import org.apache.commons.lang3.StringUtils;
import org.springframework.stereotype.Component;

import oasix.middleware.model.Port;
import oasix.middleware.model.Vulnerability;

@Component
public class PortScanService {
	public List<Port> scan(String host) {
		String output = null;
		try {
			String cmd = "sudo nmap -O " + host;

			Process p = Runtime.getRuntime().exec(cmd);

			BufferedReader stdInput = new BufferedReader(new InputStreamReader(p.getInputStream()));
			BufferedReader stdError = new BufferedReader(new InputStreamReader(p.getErrorStream()));

			// read the output from the command
			while ((output = stdInput.readLine()) != null) {
				System.out.println(output);
			}

			// read any errors from the attempted command
			System.out.println("Here is the standard error of the command (if any):\n");
			while ((output = stdError.readLine()) != null) {
				System.out.println(output);
			}

		} catch (IOException e) {
			System.out.println("exception happened - here's what I know: ");
			e.printStackTrace();
		}
		
		return parseResult(output);
	}

	private List<Port> parseResult(String output) {
		List<Port> ports  = new ArrayList<>();
		
		Scanner scanner = new Scanner(output);
		while (scanner.hasNextLine()) {
		  String line = scanner.nextLine();
		  
		  // process the line
		  if(StringUtils.isNumeric(line.substring(0, 1))){
			  Port port = new Port();
			  String[] splitline = line.split(" ");
			  
			  String[] splitPort = splitline[0].split("/");
		  }
		}
		scanner.close();
		
		return ports;
	}
}
