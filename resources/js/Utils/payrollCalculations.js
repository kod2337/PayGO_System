export const calculateDeductions = (basicSalary) => {
    // SSS Computation (4.5% employee share)
    const sssRate = 0.045;
    const sssMaxSalary = 30000;
    const sssContribution = Math.min(basicSalary, sssMaxSalary) * sssRate;

    // PhilHealth Computation (2.5% employee share)
    const philhealthRate = 0.025;
    const philhealthMin = 10000;
    const philhealthMax = 100000;
    const philhealthSalary = Math.min(Math.max(basicSalary, philhealthMin), philhealthMax);
    const philhealthContribution = philhealthSalary * philhealthRate;

    // Pag-IBIG Computation (2% employee share)
    const pagibigRate = 0.02;
    const pagibigMaxContribution = 100;
    const pagibigContribution = Math.min(basicSalary * pagibigRate, pagibigMaxContribution);

    // Total Mandatory Deductions
    const totalMandatoryDeductions = sssContribution + philhealthContribution + pagibigContribution;

    // Taxable Income
    const taxableIncome = basicSalary - totalMandatoryDeductions;

    // Compute Withholding Tax (Based on TRAIN Law)
    let tax = 0;
    const monthlyTaxableIncome = taxableIncome;

    if (monthlyTaxableIncome <= 20833) { // 250,000 / 12
        tax = 0;
    } else if (monthlyTaxableIncome <= 33333) { // 400,000 / 12
        tax = (monthlyTaxableIncome - 20833) * 0.20;
    } else if (monthlyTaxableIncome <= 66667) { // 800,000 / 12
        tax = 2500 + (monthlyTaxableIncome - 33333) * 0.25;
    } else if (monthlyTaxableIncome <= 166667) { // 2,000,000 / 12
        tax = 10833.33 + (monthlyTaxableIncome - 66667) * 0.30;
    } else if (monthlyTaxableIncome <= 666667) { // 8,000,000 / 12
        tax = 40833.33 + (monthlyTaxableIncome - 166667) * 0.32;
    } else {
        tax = 200833.33 + (monthlyTaxableIncome - 666667) * 0.35;
    }

    return {
        sss: sssContribution,
        philhealth: philhealthContribution,
        pagibig: pagibigContribution,
        tax: tax,
        totalDeductions: totalMandatoryDeductions + tax,
        taxableIncome: taxableIncome,
        taxBracket: getTaxBracket(monthlyTaxableIncome)
    };
};

// Helper function to determine tax bracket description
const getTaxBracket = (monthlyTaxableIncome) => {
    if (monthlyTaxableIncome <= 20833) {
        return 'Tax Exempt (0%)';
    } else if (monthlyTaxableIncome <= 33333) {
        return '20% of excess over ₱250,000 annually';
    } else if (monthlyTaxableIncome <= 66667) {
        return '₱30,000 + 25% of excess over ₱400,000 annually';
    } else if (monthlyTaxableIncome <= 166667) {
        return '₱130,000 + 30% of excess over ₱800,000 annually';
    } else if (monthlyTaxableIncome <= 666667) {
        return '₱490,000 + 32% of excess over ₱2,000,000 annually';
    } else {
        return '₱2,410,000 + 35% of excess over ₱8,000,000 annually';
    }
};