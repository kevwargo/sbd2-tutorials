/*
 * To change this license header, choose License Headers in Project Properties.
 * To change this template file, choose Tools | Templates
 * and open the template in the editor.
 */

// package jdbctutorial;

import java.sql.*;
import java.util.Properties;
import java.util.logging.Level;
import java.util.logging.Logger; 

/**
 *
 * @author scott
 */
public class JDBCTutorial {

    public static void zadanie1(Connection conn) throws SQLException
    {
        Statement stmt = conn.createStatement();
        ResultSet rs = stmt.executeQuery("select count(*) from pracownicy");
        if (rs.next())
            System.out.printf("Zatrudniono %d pracowników, w tym:\n", rs.getInt(1));
        rs.close();
        rs = stmt.executeQuery("select id_zesp, count(*) as cnt from pracownicy group by id_zesp");
        while (rs.next())
        {
            System.out.printf("%d w zespole %d\n", rs.getInt("cnt"), rs.getInt("id_zesp"));
        }
        rs.close();
        stmt.close();
    }

    public static void zadanie2(Connection conn) throws SQLException
    {
        Statement stmt = conn.createStatement(ResultSet.TYPE_SCROLL_SENSITIVE,
                                              ResultSet.CONCUR_READ_ONLY);
        ResultSet rs = stmt.executeQuery("select * from pracownicy " +
                                         "where etat='ASYSTENT' " +
                                         "order by placa_pod DESC");
        rs.afterLast();
        if (rs.previous())
            System.out.printf("najmniej zarabia asystent %s\n", rs.getString("nazwisko"));
        if (rs.relative(-3))
            System.out.printf("trzeci najmniej zarabiający asystent to %s\n", rs.getString("nazwisko"));
        if (rs.relative(2))
            System.out.printf("przedostatni w rankingu najmniej zarabiających asystentów to %s\n", rs.getString("nazwisko"));
        rs.close();
        stmt.close();
    }

    private static int generateEmployeeNextId(Statement stmt) throws SQLException
    {
        ResultSet rs = stmt.executeQuery("select max(id_prac) from pracownicy");
        rs.first();
        int nextId = rs.getInt(1) + 10;
        rs.close();
        return nextId;
    }
    
    public static void zadanie3(Connection conn) throws SQLException
    {
        Statement stmt = conn.createStatement();
        int [] zwolnienia = {150, 200, 230};
        String [] zatrudnienia = {"Kandefer", "Rygiel", "Boczar"};
        
        for (int i = 0; i < zwolnienia.length; i++)
        {
            int changes = stmt.executeUpdate("delete from pracownicy where id_prac = " + Integer.toString(zwolnienia[i]));
            System.out.printf("usunięto pracownika o identyfikatorze %d\n", zwolnienia[i]);
        }
        for (int i = 0; i < zatrudnienia.length; i++)
        {
            int changes = stmt.executeUpdate(
                "insert into pracownicy(id_prac, nazwisko) " +
                "values(" + Integer.toString(generateEmployeeNextId(stmt)) +
                ", '" + zatrudnienia[i] + "')");
            System.out.printf("dodano pracownika %s\n", zatrudnienia[i]);
        }
        stmt.close();
    }

    public static void zadanie4(Connection conn) throws SQLException
    {
        conn.setAutoCommit(false);
        Statement stmt = conn.createStatement();
        ResultSet rs = stmt.executeQuery("select * from etaty");
        rs.beforeFirst();
        while (rs.next())
            System.out.printf("nazwa: %s; płaca minimalna: %d; płaca maksymalna: %d.\n",
                              rs.getString("nazwa"), rs.getInt("placa_min"),
                              rs.getInt("placa_max"));
        rs.close();

        stmt.executeUpdate("insert into etaty(nazwa, placa_min, placa_max) values('NIKT', 0, 5)");

        rs = stmt.executeQuery("select * from etaty");
        rs.beforeFirst();
        while (rs.next())
            System.out.printf("nazwa: %s; płaca minimalna: %d; płaca maksymalna: %d.\n",
                              rs.getString("nazwa"), rs.getInt("placa_min"),
                              rs.getInt("placa_max"));
        rs.close();

        conn.rollback();

        rs = stmt.executeQuery("select * from etaty");
        rs.beforeFirst();
        while (rs.next())
            System.out.printf("nazwa: %s; płaca minimalna: %d; płaca maksymalna: %d.\n",
                              rs.getString("nazwa"), rs.getInt("placa_min"),
                              rs.getInt("placa_max"));
        rs.close();

        stmt.executeUpdate("insert into etaty(nazwa, placa_min, placa_max) values('KTOS', 100, 500)");

        conn.commit();
        
        rs = stmt.executeQuery("select * from etaty");
        rs.beforeFirst();
        while (rs.next())
            System.out.printf("nazwa: %s; płaca minimalna: %d; płaca maksymalna: %d.\n",
                              rs.getString("nazwa"), rs.getInt("placa_min"),
                              rs.getInt("placa_max"));
        rs.close();
        stmt.close();
    }

    public static void zadanie5(Connection conn) throws SQLException
    {
        PreparedStatement pstmt = conn.prepareStatement(
            "insert into pracownicy(id_prac, nazwisko, etat, placa_pod) " +
            "values(?, ?, ?, ?)");
        PreparedStatement primkeygen =
            conn.prepareStatement("select max(id_prac) from pracownicy");
        String [] nazwiska = {"Wozniak", "Dabrowski", "Kozlowski"};
        int [] place = {1300, 1700, 1500};
        String [] etaty = {"ASYSTENT", "PROFESOR", "ADIUNKT"};

        for (int i = 0; i < 3; i++)
        {
            ResultSet rs = primkeygen.executeQuery();
            rs.first();
            int id_prac = rs.getInt(1);
            rs.close();
            pstmt.setInt(1, id_prac);
            pstmt.setString(2, nazwiska[i]);
            pstmt.setInt(3, place[i]);
            pstmt.setString(4, etaty[i]);
            pstmt.executeUpdate();
        }
        primkeygen.close();
        pstmt.close();
    }

    public static void zadanie6(Connection conn) throws SQLException
    {
        conn.setAutoCommit(false);
        Statement stmt = conn.createStatement();
        int id_prac = generateEmployeeNextId(stmt);
        stmt.close();
        PreparedStatement pstmt = conn.prepareStatement("insert into pracownicy(id_prac, nazwisko) values(?, ?)");

        long start = System.nanoTime();
        for (int i = 0; i < 2000; i++)
        {
            pstmt.setInt(1, id_prac);
            pstmt.setString(2, "Pracownik" + Integer.toString(id_prac++));
            pstmt.executeUpdate();
        }
        long total = System.nanoTime() - start;
        System.out.printf("Czas sekwencyjnego wykonywania poleceń: %lf sekund\n", (double)total / 1000000000.0);
        pstmt.close();

        pstmt = conn.prepareStatement("insert into pracownicy(id_prac, nazwisko) values(?, ?)");
        start = System.nanoTime();
        for (int i = 0; i < 2000; i++)
        {
            pstmt.setInt(1, id_prac);
            pstmt.setString(2, "Pracownik" + Integer.toString(id_prac++));
            pstmt.addBatch();
        }
        pstmt.executeBatch();
        total = System.nanoTime() - start;
        System.out.printf("Czas wykonywania polecenia wsadowego: %lf sekund\n", (double)total / 1000000000.0);

        pstmt.close();
    }

    public static void zadanie7(Connection conn) throws SQLException
    {
        CallableStatement cstmt = conn.prepareCall("{? = call changecase(?, ?)}");
        cstmt.setInt(2, 220);
        cstmt.registerOutParameter(1, Types.INTEGER);
        cstmt.registerOutParameter(3, Types.VARCHAR);
        cstmt.execute();
        int result = cstmt.getInt(1);
        if (result == 1)
        {
            String nazwisko = cstmt.getString(3);
            System.out.printf("Wywołanie funkcji powiodło się: %s\n", nazwisko);
        }
        else
            System.out.printf("Niepoprawny identyfikator pracownika\n");
        cstmt.close();
    }
    
    public static void simpleQuery(Connection conn) throws SQLException
    {
        Statement stmt = conn.createStatement();
        ResultSet rs = stmt.executeQuery("select id_prac, RPAD(nazwisko, 15) as nazwisko, placa_pod from pracownicy");
        rs.afterLast();
        while (rs.previous())
        {
            System.out.printf("%d %s %d\n", rs.getInt("id_prac"),
                                            rs.getString("nazwisko"),
                                            rs.getInt("placa_pod"));
        }
        rs.close();
        stmt.close();
    }
    

    /**
     * @param args the command line arguments
     */
    public static void main(String[] args) {
        // TODO code application logic here
        Connection conn = null;
        Properties connectionProps = new Properties();
        connectionProps.put("user", "inf114816");
        connectionProps.put("password", "inf114816");
        System.out.println("lepecbeke");
        try {
            conn = DriverManager.getConnection(
                    "jdbc:oracle:thin:@//admlab2-main.cs.put.poznan.pl:1521/dblab01.cs.put.poznan.pl",
                    connectionProps);
            System.out.println("Połączono z bazą danych");
            simpleQuery(conn);
//            countObjects(conn);
            conn.close();
        } catch (SQLException ex) {
            Logger.getLogger(JDBCTutorial.class.getName()).log(Level.SEVERE,
                "Wystąpił błąd", ex);
                System.exit(-1);
        }
    }
    
}
