package oasix.middleware.service;

import java.io.BufferedReader;
import java.io.IOException;
import java.io.InputStreamReader;
import java.util.ArrayList;
import java.util.List;

import org.springframework.scheduling.annotation.Async;
import org.springframework.stereotype.Component;

import oasix.middleware.model.Vulnerability;

@Component
public class NiktoService {
	public List<Vulnerability> scan(String host) {
		List<Vulnerability> vulnerabilities = new ArrayList<>();
		int separatorCount = 0;
		String output = null;
		try {
			String cmd = "nikto -h " + host;

			Process p = Runtime.getRuntime().exec(cmd);

			BufferedReader stdInput = new BufferedReader(new InputStreamReader(p.getInputStream()));
			BufferedReader stdError = new BufferedReader(new InputStreamReader(p.getErrorStream()));

			// read the output from the command
			while ((output = stdInput.readLine()) != null) {
				System.out.println(output);
				if (output.startsWith("--")) {
					separatorCount++;
				}
				if (separatorCount >= 2) {
					if (output.startsWith("+ ")) {
						String vulnString = output.substring(1, output.length());

						String type = "PENTEST";
						String severity = "STANDARD";
						String title = vulnString;
						String description = vulnString;

						vulnerabilities.add(new Vulnerability(type, severity, title, description));
					}
				}
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

		return vulnerabilities;
	}
}
