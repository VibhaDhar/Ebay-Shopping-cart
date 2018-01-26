import java.io.File;
import java.io.FileOutputStream;
import java.io.IOException;
import java.io.InputStream;
import java.io.OutputStream;
import java.util.List;

import javax.servlet.ServletException;
import javax.servlet.http.HttpServlet;
import javax.servlet.http.HttpServletRequest;
import javax.servlet.http.HttpServletResponse;
import javax.servlet.http.HttpSession;

import org.apache.commons.fileupload.FileItem;
import org.apache.commons.fileupload.disk.DiskFileItemFactory;
import org.apache.commons.fileupload.servlet.ServletFileUpload;
import org.apache.commons.io.FilenameUtils;

import com.amazonaws.auth.AWSCredentials;
import com.amazonaws.auth.BasicAWSCredentials;
import com.amazonaws.regions.Region;
import com.amazonaws.regions.Regions;
import com.amazonaws.services.s3.AmazonS3;
import com.amazonaws.services.s3.AmazonS3Client;
import com.amazonaws.services.s3.model.CannedAccessControlList;
import com.amazonaws.services.s3.model.PutObjectRequest;
import com.amazonaws.services.simpleemail.AmazonSimpleEmailServiceClient;
import com.amazonaws.services.simpleemail.model.Body;
import com.amazonaws.services.simpleemail.model.Content;
import com.amazonaws.services.simpleemail.model.Destination;
import com.amazonaws.services.simpleemail.model.Message;
import com.amazonaws.services.simpleemail.model.SendEmailRequest;

public class EmailSenderServlet extends HttpServlet {
	
	static final String FROM = "dharvibha@gmail.com"; // Replace with your "From"
	static final String TO = "vibha.dhar@mavs.uta.edu"; // Replace with a "To"
	static final String SUBJECT = "Receipt of purchase using Amazon SES ";


	private static final long serialVersionUID = -6309555599288899548L;

	@Override
	protected void doGet(HttpServletRequest req, HttpServletResponse resp)
			throws ServletException, IOException {
		Destination destination = new Destination()
				.withToAddresses(new String[] { TO });

		Content subject = new Content().withData(SUBJECT);
		String s =" The total purchase value is " + req.getParameter("totalVal");
		Content textBody = new Content().withData(s);
		Body body = new Body().withText(textBody);
		Message message = new Message().withSubject(subject).withBody(body);
		SendEmailRequest request = new SendEmailRequest().withSource(FROM)
				.withDestination(destination).withMessage(message);

		try {
			System.out
					.println("Attempting to send an email through Amazon SES by using the AWS SDK for Java...");
			AWSCredentials credentials = new BasicAWSCredentials(
					"xxxxxxxx",
					"yyyyyyyy");

			AmazonSimpleEmailServiceClient client = new AmazonSimpleEmailServiceClient(
					credentials);
			Region REGION = Region.getRegion(Regions.US_WEST_2);
			client.setRegion(REGION);
			client.sendEmail(request);
			System.out.println("Email sent!");
			resp.getWriter().print("Your receipt has been emailed");
		} catch (Exception ex) {
			System.out.println("The email was not sent.");
			System.out.println("Error message: " + ex.getMessage());
		}

	}

	
}
