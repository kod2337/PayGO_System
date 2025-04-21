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

    // Total Deductions
    const totalDeductions = sssContribution + philhealthContribution + pagibigContribution;

    // Taxable Income
    const taxableIncome = basicSalary - totalDeductions;

    return {
        sss: sssContribution,
        philhealth: philhealthContribution,
        pagibig: pagibigContribution,
        totalDeductions: totalDeductions,
        taxableIncome: taxableIncome
    };
};